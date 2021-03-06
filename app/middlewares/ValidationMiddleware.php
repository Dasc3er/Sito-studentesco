<?php

namespace App\Middlewares;

use Respect\Validation\Exceptions\NestedValidationException;

/**
 * Validation for Slim.
 * Based on DavidePastore\Slim\Validation.
 */
class ValidationMiddleware extends \App\Middleware
{
    /**
     * Validators.
     *
     * @var array
     */
    protected $validators = [];

    /**
     * The translator to use fro the exception message.
     *
     * @var callable
     */
    protected $translator = null;

    /**
     * Errors from the validation.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Create new Validator service provider.
     *
     * @param null|array|ArrayAccess $validators
     * @param null|callable          $translator
     */
    public function __construct($container, $validators = null, $translator = null)
    {
        parent::__construct($container);

        // Set the validators
        if (is_array($validators) || $validators instanceof ArrayAccess) {
            $this->validators = $validators;
        } elseif (is_null($validators)) {
            $this->validators = [];
        }
        $this->translator = $translator;
    }

    /**
     * Validation middleware invokable class.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        $this->errors = [];
        $params = $request->getParams();
        $this->validate($params, $this->validators);

        return $next($request, $response);
    }

    /**
     * Validate the parameters by the given params, validators and actual keys.
     * This method populates the $errors attribute.
     *
     * @param array $params     The array of parameters
     * @param array $validators The array of validators
     * @param array $actualKeys An array that will save all the keys of the tree to retrieve the correct value
     */
    protected function validate($params = [], $validators = [], $actualKeys = [])
    {
        //Validate every parameters in the validators array
      foreach ($validators as $key => $validator) {
          $actualKeys[] = $key;
          $param = $this->getNestedParam($params, $actualKeys);
          if (!empty($param)) {
              if (is_array($validator)) {
                  $this->validate($params, $validator, $actualKeys);
              } else {
                  try {
                      $validator->assert($param);
                  } catch (NestedValidationException $exception) {
                      if ($this->translator) {
                          $exception->setParam('translator', $this->translator);
                      }
                      $this->errors[implode('.', $actualKeys)] = $exception->getMessages();
                      $this->flash->addMessage('errors', $exception->getMessages());
                  }
              }

            //Remove the key added in this foreach
            array_pop($actualKeys);
          }
      }
    }

    /**
     * Get the nested parameter value.
     *
     * @param array $params An array that represents the values of the parameters
     * @param array $keys   An array that represents the tree of keys to use
     *
     * @return mixed The nested parameter value by the given params and tree of keys
     */
    protected function getNestedParam($params = [], $keys = [])
    {
        if (empty($keys)) {
            return $params;
        } else {
            $firstKey = array_shift($keys);
            if (array_key_exists($firstKey, $params)) {
                $params = (array) $params;
                $paramValue = $params[$firstKey];

                return $this->getNestedParam($paramValue, $keys);
            } else {
                return;
            }
        }
    }

    /**
     * Check if there are any errors.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Get errors.
     *
     * @return array The errors array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get validators.
     *
     * @return array The validators array
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * Set validators.
     *
     * @param array $validators The validators array
     */
    public function setValidators($validators)
    {
        $this->validators = $validators;
    }

    /**
     * Get translator.
     *
     * @return callable The translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Set translator.
     *
     * @param callable $translator The translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }
}
